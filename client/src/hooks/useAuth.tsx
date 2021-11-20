import axios from 'axios';
import React, { useState, useContext, createContext } from 'react';

axios.defaults.baseURL = process.env.REACT_APP_API_BASE_URL;
axios.defaults.withCredentials = true;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

interface Auth {
  user: { name: string } | null;
  authenticated: boolean;
  register: (
    name: string,
    email: string,
    password: string,
    passwordConfirmation: string,
    deviceName: string
  ) => Promise<void> | void;
  login: (
    email: string,
    password: string,
    deviceName: string
  ) => Promise<void> | void;
  logout: () => Promise<void> | void;
}

const AuthContext = createContext<Auth>({
  user: null,
  authenticated: false,
  register: () => {},
  login: () => {},
  logout: () => {},
});

export const AuthProvider: React.FC = ({ children }) => {
  const auth = useProvideAuth();
  return <AuthContext.Provider value={auth}>{children}</AuthContext.Provider>;
};

export const useAuth = () => useContext(AuthContext);

interface AuthenticatedResponse {
  user: { name: string };
  token: string;
}

function useProvideAuth(): Auth {
  const [authenticated, setAuthenticated] = useState(false);
  const [user, setUser] = useState<{ name: string } | null>(null);

  async function register(
    name: string,
    email: string,
    password: string,
    passwordConfirmation: string,
    deviceName: string
  ) {
    const { data } = await axios.post<AuthenticatedResponse>('api/register', {
      name,
      email,
      password,
      password_confirmation: passwordConfirmation,
      device_name: deviceName,
    });

    setUser(data.user);
    axios.defaults.headers.common['Authorization'] = `Bearer ${data.token}`;
    setAuthenticated(true);
  }

  async function login(email: string, password: string, deviceName: string) {
    const { data } = await axios.post<AuthenticatedResponse>('api/login', {
      email,
      password,
      device_name: deviceName,
    });

    setUser(data.user);
    axios.defaults.headers.common['Authorization'] = `Bearer ${data.token}`;
    setAuthenticated(true);
  }

  async function logout() {
    await axios.delete('api/logout');

    setUser(null);
    axios.defaults.headers.common['Authorization'] = '';
    setAuthenticated(false);
  }

  return {
    user,
    authenticated,
    register,
    login,
    logout,
  };
}
