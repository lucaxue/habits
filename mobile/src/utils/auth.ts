import axios from 'axios';

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
