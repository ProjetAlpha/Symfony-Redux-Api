const defaultState = {
    image: [],
    loading: false,
  }
  
  export default function Image(state = defaultState, action = {}) {
    switch (action.type) {

      case 'FETCH_IMAGE': {
        return {
          ...state,
          image: [...state.image, action.data],
          loading: false,
        }
      }
  
      case 'UPLOAD_IMAGE': {
        const image = action.payload.data;
        return {
          ...state,
          image: [...state.image, action.data],
          loading: false
        }
      }
  
      case 'UPLOAD_IMAGE_REJECTED': {
        const data = action.payload.response.data;
        const errors = { global: data.message };
        return {
          ...state,
          loading: false
        }
      }
  
      case 'DELETE_IMAGE': {
        const _id = action.data.id;
        return {
          ...state,
          image: state.image.filter(item => item._id !== _id)
        }
      }
  
      default:
        return state;
    }
  }