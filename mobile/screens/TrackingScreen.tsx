import * as React from 'react';
import { useState } from 'react';
import { Button, Switch } from 'react-native';
import tailwind from 'tailwind-rn';

import { Text, View } from '../components/Themed';
import { RootTabScreenProps } from '../types';

const dummyHabits = [
  {
    id: 'some-uuid-1',
    name: 'Read Book',
    streak: 'P0Y0M0D',
    completed: false,
  },
  {
    id: 'some-uuid-2',
    name: 'Learning Arabic',
    streak: 'P0Y0M0D',
    completed: false,
  },
];

type Streak = { years: number; months: number; days: number };

function convertToStreak(streak: string): Streak {
  const matches = streak.match(/P(\d+)Y(\d+)M(\d+)D/);
  if (!matches || matches.length !== 4) {
    throw new Error(`Invalid streak format: ${streak}`);
  }
  const [, years, months, days] = matches;
  return { years: +years, months: +months, days: +days };
}

export default function TrackingScreen({
  navigation,
}: RootTabScreenProps<'TabOne'>) {
  const [habits, setHabits] = useState(dummyHabits);

  function toggleHabitCompletion(id: string) {
    let habit = habits.find(h => h.id === id);
    if (!habit) {
      throw Error(`Habit of id ${id} not found`);
    }
    habit.completed = !habit.completed;
    setHabits([...habits.filter(h => h.id !== id), habit]);
  }

  return (
    <View style={tailwind('flex-1 justify-center items-center')}>
      <Text style={tailwind('text-4xl font-bold')}>Habits</Text>
      <View
        style={tailwind('my-4 h-1 w-4/5')}
        lightColor='#eee'
        darkColor='rgba(255,255,255,0.1)'
      />
      {habits.map(habit => (
        <View key={habit.id} style={tailwind('flex-row py-2 px-6 w-full')}>
          <Switch
            value={habit.completed}
            onValueChange={() => toggleHabitCompletion(habit.id)}
          />
          <View>
            <Text style={tailwind('font-bold text-lg')}>{habit.name}</Text>
            <Text>{JSON.stringify(convertToStreak(habit.streak))}</Text>
          </View>
        </View>
      ))}
    </View>
  );
}
