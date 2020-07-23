import client from '.';

const baseUrl = '/admin';

export const getUsers = () => {
    return dispatch => {
        client.get(`${baseUrl}/users/fetch`).then(res => {
        dispatch({ type: 'GET_USERS', data: res.data })
      }
      ).catch(err => {
          dispatch({ type: 'ADD_ERROR', error: err.response.data })
        }
      )
    }
}

export const removeUserById = id => {
    return dispatch => {
        client.delete(`${baseUrl}/users/delete`, { id: id }).then(res => {
        dispatch({ type: 'REMOVE_USER', data: res.data })
      }
      ).catch(err => {
          dispatch({ type: 'ADD_ERROR', error: err.response.data })
        }
      )
    }
}