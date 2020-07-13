import client from '.';
import * as Auth from '../utils/Authentification';

console.log(Auth);

export const login = user => {
    return dispatch => {
        client.post('login', {
            email: user.email,
            password: user.password
      }).then(res => {
        Auth.setUser(res.data);
        dispatch({ type: 'LOGIN', data: res.data })
      }
      ).catch(err => {
          Auth.logout();
          dispatch({ type: 'ADD_ERROR', error: err })
        }
      )
    }
}

export const register = user => {
    return dispatch => {
        console.log(user);
        client.post('register', {
            email: user.email,
            password: user.password,
            firstname: user.firstname,
            lastname: user.lastname
      }).then(res => {
          dispatch({ type: 'LOGIN', data: res.data })
        }
      ).catch(err => {
          dispatch({ type: 'ADD_ERROR', error: err })
        }
      )
    }
}

export const logout = user => {
    return dispatch => {
        client.get('logout').then(res => {
          Auth.logout();
          dispatch({ type: 'LOGOUT', data: res.data })
        }
      ).catch(err =>
        dispatch({ type: 'ADD_ERROR', error: err })
      )
    }
}