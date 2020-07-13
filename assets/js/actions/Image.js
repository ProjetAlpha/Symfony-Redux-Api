import client from '.';

const baseUrl = 'api/image';

export const fetchImage = user => {
  return dispatch => {
      client.get(`${baseUrl}/searchImage`, {
          email: user.email
      }).then(res => 
        dispatch({ type: 'FETCH_IMAGE', data: res.data })
      ).catch(err =>
        dispatch({ type: 'ADD_ERROR', error: err })
      )
  }
}

export const uploadImage = (user, image) => {
  return dispatch => {
      client.post(`${baseUrl}/upload`, {
          name: image.name,
          email: user.email,
          base64_image: image.base64_image,
          extension: image.extension
      }).then(res =>
        dispatch({ type: 'UPLOAD_IMAGE', data: res.data })
      ).catch(err =>
        dispatch({ type: 'ADD_ERROR', error: err })
      )
  }
}

export const deleteImage = image => {
  return dispatch => {
      client.delete(`${baseUrl}/delete`, {
          img_id: image.id
    }).then(res =>
      dispatch({ type: 'DELETE_IMAGE', data: res.data })
    ).catch(err =>
      dispatch({ type: 'ADD_ERROR', error: err })
    )
  }
}