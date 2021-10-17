import axios from 'axios';

export async function register(
  name: string,
  email: string,
  password: string,
  passwordConfirmation: string
): Promise<boolean> {
  try {
    await axios.post('register', {
      name: name,
      email: email,
      password: password,
      password_confirmation: passwordConfirmation,
    });
    return true;
  } catch (err) {
    return false;
  }
}

export async function login(
  email: string,
  password: string
): Promise<{} | null> {
  try {
    await axios.get('sanctum/csrf-cookie');
    await axios.post('login', { email, password });
    const res = await axios.get('api/user');
    return res.data;
  } catch (err) {
    return null;
  }
}

export async function logout(): Promise<boolean> {
  try {
    await axios.post('logout');
    return true;
  } catch (err) {
    return false;
  }
}
