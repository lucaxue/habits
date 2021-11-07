import * as React from 'react';
import { Button } from 'react-native';
import tailwind from 'tailwind-rn';
import { Text, View } from '../components/Themed';
import { useAuth } from '../hooks/useAuth';

export default function SettingsScreen() {
  const { user, logout } = useAuth();

  return (
    <View style={tailwind('flex-1 items-center bg-blue-100')}>
      <View
        style={tailwind(
          'rounded-b-3xl h-1/3 w-full flex justify-center items-center bg-indigo-500'
        )}
      >
        <Text style={tailwind('text-4xl font-bold text-white')}>Settings</Text>
      </View>
      <View
        style={tailwind(
          'flex items-center p-10 h-full mt-20 rounded-t-3xl w-full'
        )}
      >
        <Text>{JSON.stringify(user, null, 2)}</Text>
        <Button onPress={async () => await logout()} title='Logout' />
      </View>
    </View>
  );
}
