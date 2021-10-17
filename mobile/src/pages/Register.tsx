import React, { useState } from 'react';
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
import { useAuth } from '../utils/useAuth';
import { Redirect } from 'react-router';

export const Register: React.FC = () => {
  const { user, authenticated, register, logout } = useAuth();

  const [registered, setRegistered] = useState(false);

  const [name, setName] = useState('Jane Doe');
  const [email, setEmail] = useState('jane@example.com');
  const [password, setPassword] = useState('ApplePear1234.');
  const [passwordConfirmation, setPasswordConfirmation] =
    useState('ApplePear1234.');

  if (registered) {
    return <Redirect to='/login'/>;
  }

  return (
    <IonPage>
      <IonHeader>
        <IonToolbar>
          <IonTitle>Register</IonTitle>
        </IonToolbar>
      </IonHeader>
      <IonContent fullscreen>
        <IonHeader collapse='condense'>
          <IonToolbar>
            <IonTitle size='large'>Register</IonTitle>
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
                  value={name}
                  placeholder='Name'
                  onIonChange={e => setName(e.detail.value!)}
                ></IonInput>
              </IonItem>
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
              <IonItem>
                <IonInput
                  type='password'
                  value={passwordConfirmation}
                  placeholder='Confirm Password'
                  onIonChange={e => setPasswordConfirmation(e.detail.value!)}
                ></IonInput>
              </IonItem>
              <IonButton
                expand='block'
                onClick={async () => {
                  const registered = await register(
                    name,
                    email,
                    password,
                    passwordConfirmation
                  );
                  setRegistered(registered);
                }}
              >
                Register
              </IonButton>
            </>
          ) : (
            <IonButton
              expand='block'
              onClick={async () => {
                await logout();
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
