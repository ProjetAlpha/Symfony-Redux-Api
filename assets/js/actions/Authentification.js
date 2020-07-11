import { client } from '.';
import { setUser } from '../utils/Authentification';

export const login = user => {
    return dispatch => {
        client.post('login', {
          params: {
            email: user.email,
            password: user.password
          }
      }).then(res =>
        dispatch({ type: 'LOGIN', data: res.body })
      ).catch(err =>
        dispatch({ type: 'ADD_ERROR', error: err })
      )
    }
}

export const register = user => {
    return dispatch => {
        client.post('login', {
          params: {
            email: user.email,
            password: user.password
          }
      }).then(res =>
        dispatch({ type: 'LOGIN', data: res.body }) && setUser(user)
      ).catch(err =>
        dispatch({ type: 'ADD_ERROR', error: err })
      )
    }
}

export const logout = user => {
    return dispatch => {
        client.post('logout').then(res =>
        dispatch({ type: 'LOGOUT', data: res.body })
      ).catch(err =>
        dispatch({ type: 'ADD_ERROR', error: err })
      )
    }
}