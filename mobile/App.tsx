import { StatusBar } from 'expo-status-bar';
import React from 'react';
import { SafeAreaProvider } from 'react-native-safe-area-context';

import useCachedResources from './hooks/useCachedResources';
import useColorScheme from './hooks/useColorScheme';
import Navigation from './navigation';
import LoginScreen from './screens/LoginScreen';
import { AuthProvider, useAuth } from './hooks/useAuth';

export default function App() {
  const isLoadingComplete = useCachedResources();
  const colorScheme = useColorScheme();

  if (!isLoadingComplete) {
    return null;
  }

  return (
    <AuthProvider>
      <SafeAreaProvider>
        <Container colorScheme={colorScheme} />
        <StatusBar backgroundColor='black' />
      </SafeAreaProvider>
    </AuthProvider>
  );
}

function Container(colorScheme: any) {
  const { authenticated } = useAuth();

  if (!authenticated) {
    return <LoginScreen />;
  }

  return <Navigation colorScheme={colorScheme} />;
}
