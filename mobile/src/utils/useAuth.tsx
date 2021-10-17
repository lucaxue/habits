import React, { useState, useContext, createContext } from 'react';
import {
  login as axiosLogin,
  logout as axiosLogout,
  register as axiosRegister,
} from './auth';

interface Auth {
  user: {} | null;
  authenticated: boolean;
  register: (
    name: string,
    email: string,
    password: string,
    passwordConfirmation: string
  ) => Promise<boolean> | false;
  login: (email: string, password: string) => Promise<void> | void;
  logout: () => Promise<void> | void;
}

const AuthContext = createContext<Auth>({
  user: null,
  authenticated: false,
  register: () => false,
  login: () => {},
  logout: () => {},
});

export const AuthProvider: React.FC = ({ children }) => {
  const auth = useProvideAuth();
  return <AuthContext.Provider value={auth}>{children}</AuthContext.Provider>;
};

export const useAuth = () => useContext(AuthContext);

function useProvideAuth(): Auth {
  const [authenticated, setAuthenticated] = useState(false);
  const [user, setUser] = useState<{} | null>(null);

  async function register(
    name: string,
    email: string,
    password: string,
    passwordConfirmation: string
  ) {
    return await axiosRegister(name, email, password, passwordConfirmation);
  }

  async function login(email: string, password: string) {
    const user = await axiosLogin(email, password);
    setUser(user);
    setAuthenticated(true);
  }

  async function logout() {
    await axiosLogout();
    setUser(null);
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
