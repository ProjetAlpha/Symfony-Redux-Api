import client from '.';

const baseUrl = 'image';

export const fetchImage = async id => {
  try {
    return await client.get(`${baseUrl}/private/${id}`);
  } catch(error) {
    // dispatch({ type: 'ADD_ERROR', error: err.response.data })
  }
}

export const uploadImage = (user, image) => {
  return dispatch => {
    client.post(`${baseUrl}/upload`, image).then(res =>
      dispatch({ type: 'UPLOAD_IMAGE', data: res.data })
    ).catch(err =>
      dispatch({ type: 'ADD_ERROR', error: err.response.data })
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
      dispatch({ type: 'ADD_ERROR', error: err.response.data })
    )
  }
}