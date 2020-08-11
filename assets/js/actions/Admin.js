import client from './index';

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
      client.delete(`${baseUrl}/users/delete/${id}`).then(res => {
          dispatch({ type: 'REMOVE_USER', data: res.data })
      }
      ).catch(err => {
          dispatch({ type: 'ADD_ERROR', error: err.response.data })
        }
      )
    }
}

export const createArticle = (id, data) => {
  return dispatch => {
    client.post(`${baseUrl}/${id}/articles/create`, data).then(res => {
      dispatch({ type: 'ADMIN_CREATE_ARTICLE', data: res.data })
      dispatch({ type: 'REQUEST_SUCCESS' })
    }
    ).catch(err =>
        dispatch({ type: 'ADD_ERROR', error: err.response.data })
    )
  }
}

export const updateArticle = (adminId, articleId, data) => {
  return dispatch => {
    client.post(`${baseUrl}/${adminId}/articles/${articleId}/update`, data).then(res => {
      dispatch({ type: 'ADMIN_UPDATE_ARTICLE', data: res.data  })
      dispatch({ type: 'REQUEST_SUCCESS' })
    }
    ).catch(err =>
        dispatch({ type: 'ADD_ERROR', error: err.response.data })
    )
  }
}

export const fetchArticle = (adminId, articleId) => {
  return dispatch => {
    client.get(`${baseUrl}/${adminId}/articles/${articleId}`).then(res => {
      dispatch({ type: 'ADMIN_FETCH_ARTICLE' })
      dispatch({ type: 'REQUEST_SUCCESS' })
    }
    ).catch(err =>
        dispatch({ type: 'ADD_ERROR', error: err.response.data })
    )
  }
}

export const fetchAllArticle = (adminId, data) => {
  return dispatch => {
    client.post(`${baseUrl}/${adminId}/articles`, data).then(res => {
      dispatch({ type: 'ADMIN_FETCH_ALL_ARTICLE', data: res.data })
    }).catch(err =>  dispatch({ type: 'ADD_ERROR', error: err.response.data }))
  }
}