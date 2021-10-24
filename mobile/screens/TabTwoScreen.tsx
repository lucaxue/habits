import * as React from 'react';
import tailwind from 'tailwind-rn';

import { Text, View } from '../components/Themed';

export default function TabTwoScreen() {

  return (
    <View style={tailwind('flex-1 justify-center items-center')}>
      <Text style={tailwind('text-4xl font-bold')}>Tab Two</Text>
      <View
        style={tailwind('my-4 h-1 w-4/5')}
        lightColor='#eee'
        darkColor='rgba(255,255,255,0.1)'
      />
    </View>
  );
}