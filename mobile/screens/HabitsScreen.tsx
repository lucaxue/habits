import axios from 'axios';
import * as React from 'react';
import { useState } from 'react';
import { ScrollView, Switch } from 'react-native';
import tailwind from 'tailwind-rn';

import { Text, View } from '../components/Themed';
import { RootTabScreenProps } from '../types';

interface Habit {
  id: string;
  name: string;
  streak: string;
  completed: boolean;
  frequency: {
    type: string;
    days?: string;
  };
}

export default function HabitsScreen({
  navigation,
}: RootTabScreenProps<'Habits'>) {
  const [habits, setHabits] = useState<Habit[]>([]);

  React.useEffect(() => {
    (async function getHabits() {
      const { data } = await axios.get<Habit[]>('api/habits/today');
      setHabits(data);
    })();
  }, []);

  async function completeHabit(id: string) {
    const { data } = await axios.put<Habit>(`api/habits/${id}/complete`);
    setHabits([...habits.filter(({ id }) => id !== data.id), data]);
  }

  async function incompleteHabit(id: string) {
    const { data } = await axios.put<Habit>(`api/habits/${id}/incomplete`);
    setHabits([...habits.filter(({ id }) => id !== data.id), data]);
  }

  return (
    <View style={tailwind('flex-1 justify-center bg-blue-100')}>
      <View
        style={tailwind(
          'rounded-b-3xl h-1/3 w-full flex justify-center items-center bg-indigo-500'
        )}
      >
        <Text style={tailwind('text-4xl font-bold text-white')}>Habits</Text>
      </View>
      <ScrollView style={tailwind('rounded-t-3xl mt-20 pt-10 bg-white')}>
        {habits.map(habit => (
          <View
            key={habit.id}
            style={tailwind('flex-row py-6 px-12 w-full items-center')}
          >
            <Switch
              style={tailwind('mr-5')}
              value={habit.completed}
              onChange={() =>
                habit.completed
                  ? incompleteHabit(habit.id)
                  : completeHabit(habit.id)
              }
            />
            <View>
              <Text style={tailwind('font-bold text-lg w-2/3')}>
                {habit.name}
              </Text>
              <Text style={tailwind('text-sm')}>{habit.streak}</Text>
            </View>
          </View>
        ))}
      </ScrollView>
    </View>
  );
}
