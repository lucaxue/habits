import { CapacitorConfig } from '@capacitor/cli';

const config: CapacitorConfig = {
  appId: 'com.habits.app',
  appName: 'habits',
  webDir: 'build',
  bundledWebRuntime: false,
  server: {
    "url": "http://localhost:3000",
    "cleartext": true
  },
};

export default config;
