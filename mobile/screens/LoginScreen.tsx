import * as React from 'react';
import { Button, TextInput } from 'react-native';
import tailwind from 'tailwind-rn';
import { Text, View } from '../components/Themed';
import { useAuth } from '../hooks/useAuth';

export default function LoginScreen() {
  const [email, setEmail] = React.useState('john@example.com');
  const [password, setPassword] = React.useState('password');
  const { login } = useAuth();

  return (
    <View style={tailwind('flex-1 justify-center items-center')}>
      <Text style={tailwind('text-4xl font-bold mb-10')}>Login</Text>
      <View style={tailwind('w-4/5')}>
        <TextInput
          style={tailwind('border-2 border-blue-300 rounded-lg p-4 mb-6')}
          onChangeText={setEmail}
          value={email}
          placeholder='Email'
        />
        <TextInput
          style={tailwind('border-2 border-blue-300 rounded-lg p-4 mb-6')}
          onChangeText={setPassword}
          value={password}
          placeholder='Password'
        />
        <Button
          onPress={async () => await login(email, password, 'iPhone 13 Max')}
          title='Login'
        />
      </View>
    </View>
  );
}
