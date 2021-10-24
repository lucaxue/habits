import axios from 'axios';
import * as React from 'react';
import { Button, TextInput } from 'react-native';
import tailwind from 'tailwind-rn';

import { Text, View } from '../components/Themed';

axios.defaults.baseURL = 'http://localhost:8000';

export default function TabTwoScreen() {
  const [email, setEmail] = React.useState('john@example.com');
  const [password, setPassword] = React.useState('password');

  const [response, setResponse] = React.useState('');

  return (
    <View style={tailwind('flex-1 justify-center items-center')}>
      <Text style={tailwind('text-4xl font-bold')}>Tab Two</Text>
      <View
        style={tailwind('my-4 h-1 w-4/5')}
        lightColor='#eee'
        darkColor='rgba(255,255,255,0.1)'
      />
      <View style={tailwind('w-4/5')}>
        <TextInput
          style={tailwind('border-2 border-blue-300 rounded p-2 mb-2')}
          onChangeText={setEmail}
          value={email}
          placeholder='Email'
        />
        <TextInput
          style={tailwind('border-2 border-blue-300 rounded p-2 mb-2')}
          onChangeText={setPassword}
          value={password}
          placeholder='Password'
        />
        <Button
          onPress={() => {
            axios
              .post('api/sanctum/token', {
                email,
                password,
                device_name: 'iPhone 13 Max',
              })
              .then(response => {
                axios.defaults.headers.common[
                  'Authorization'
                ] = `Bearer ${response.data}`;

                setResponse(
                  JSON.stringify(axios.defaults.headers.common['Authorization'])
                );
              });
          }}
          title='login'
        />
      </View>
      <Text>{response}</Text>
    </View>
  );
}
