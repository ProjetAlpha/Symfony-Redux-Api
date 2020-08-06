import client from '.';
import * as Auth from '../utils/Authentification';

export const confirmUserAccount = id => {
    return dispatch => {
      client.get(`/register/confirmation/${id}`)
            .then(res => {
                Auth.setUser({ isConfirmed: true });
                dispatch({ type: 'CONFIRM_ACCOUNT' });
            })
            .catch(err => dispatch({ type: 'ADD_ERROR', error: err.response.data }))
    }
}

export const resetPassword = id => {
    return dispatch => {
        client.post(`/public/reset/password/${id}`)
            .then(res => {
                dispatch({ type: 'RESET_PASSWORD' });
        })
            .catch(err => dispatch({ type: 'ADD_ERROR', error: err.response.data }))
    };
}

export const sendResetPasswordLink = (email) => {
    return dispatch => {
        client.post(`/public/reset/send`, {
            email: email
        })
            .then(res => {
                dispatch({ type: 'SENT_RESET_PASSWORD' });
                dispatch({ type: 'REGISTER_SUCCESS' });
        })
            .catch(err => dispatch({ type: 'ADD_ERROR', error: err.response.data }))
    };
}

// /public/reset/password/{id}/confirm
export const confirmPasswordReset = (id, password) => {
    return dispatch => {
        client.post(`/public/reset/password/${id}/confirm`, {
            password: password
        })
            .then(res => {
                dispatch({ type: 'CONFIRM_RESET_PASSWORD' });
                dispatch({ type: 'REGISTER_SUCCESS' });
        })
            .catch(err => dispatch({ type: 'ADD_ERROR', error: err.response.data }));
    };
}

export const resetSuccess = () => {
    return dispatch => {
      dispatch({ type: 'RESET_SUCCESS'})
    }
}