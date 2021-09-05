import React, { useEffect, useState } from 'react';
import axios from 'axios';
import {
  IonButton,
  IonContent,
  IonHeader,
  IonInput,
  IonItem,
  IonPage,
  IonTitle,
  IonToolbar,
} from '@ionic/react';
import { login, logout } from '../utils/auth';

export const Home: React.FC = () => {
  const [email, setEmail] = useState('john@example.com');
  const [password, setPassword] = useState('password');

  const [authenticated, setAuthenticated] = useState(false);
  const [user, setUser] = useState<{} | null>({});

  useEffect(() => {
    authenticated &&
      (async () => {
        const user = await axios.get('/api/user');
        setUser(user.data);
      })();
  }, [authenticated]);

  return (
    <IonPage>
      <IonHeader>
        <IonToolbar>
          <IonTitle>Home</IonTitle>
        </IonToolbar>
      </IonHeader>
      <IonContent fullscreen>
        <IonHeader collapse='condense'>
          <IonToolbar>
            <IonTitle size='large'>Home</IonTitle>
          </IonToolbar>
        </IonHeader>

        <div className='absolute top-1/2 w-full transform -translate-y-1/2'>
          {authenticated && (
            <pre>
              {user ? JSON.stringify(user, null, 2) : 'loading user...'}
            </pre>
          )}

          {!authenticated ? (
            <>
              <IonItem>
                <IonInput
                  type='email'
                  value={email}
                  placeholder='Email'
                  onIonChange={e => setEmail(e.detail.value!)}
                ></IonInput>
              </IonItem>
              <IonItem>
                <IonInput
                  type='password'
                  value={password}
                  placeholder='Password'
                  onIonChange={e => setPassword(e.detail.value!)}
                ></IonInput>
              </IonItem>
              <IonButton
                expand='block'
                onClick={async () => {
                  await login(email, password);
                  setAuthenticated(true);
                }}
              >
                Login
              </IonButton>
            </>
          ) : (
            <IonButton
              expand='block'
              onClick={async () => {
                await logout();
                setAuthenticated(false);
                setUser(null);
              }}
            >
              Logout
            </IonButton>
          )}
        </div>
      </IonContent>
    </IonPage>
  );
};
