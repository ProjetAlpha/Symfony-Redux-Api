import client from '.';
import * as Auth from '../utils/Authentification';

export const login = user => {
    return dispatch => {
        client.post('login', {
            email: user.email,
            password: user.password
      }).then(res => {
        Auth.setUser(res.data);
        console.log(res.data.token);
        Auth.setAuthToken(client, res.data.token);
        dispatch({ type: 'LOGIN', data: res.data })
      }
      ).catch(err => {
          Auth.logout();
          dispatch({ type: 'ADD_ERROR', error: err.response.data })
        }
      )
    }
}

export const register = user => {
    return dispatch => {
        client.post('register', {
            email: user.email,
            password: user.password,
            firstname: user.firstname,
            lastname: user.lastname
      }).then(res => {
          Auth.setUser({ confirmationLink: res.data.confirmationLink })
          dispatch({ type: 'REGISTER', data: res.data })
        }
      ).catch(err => {
          dispatch({ type: 'ADD_ERROR', error: err.response.data })
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
        dispatch({ type: 'ADD_ERROR', error: err.response.data })
      )
    }
}

export const resetSuccess = () => {
  return dispatch => {
    dispatch({ type: 'RESET_SUCCESS'})
  }
}

export const setAuthToken = (axios, token) => {
  axios.defaults.headers.common['Authorization'] = '';
  delete axios.defaults.headers.common['Authorization'];

  if (token) {
    axios.defaults.headers.common['Authorization'] = `${token}`;
  }
}