export const getProfilData = user => {
    return dispatch => {
      client.post(`${baseUrl}/profil`, {
        id: user.id
      }).then(res => {
        dispatch({ type: 'FETCH_PROFIL_DATA', data: res.data })
      }).catch(err => {
        dispatch({ type: 'ADD_ERROR', error: err })
      });
    }
}