const initialState = {
    content: null,
}
  
export default function Admin(state = initialState, action = {}) {
    switch (action.type) {
     
      case 'SET_DATA': {
        return {
          ...state,
          content: action.data
        }
      }
  
      case 'UNSET_DATA': {
        return {
            content: null
        }
      }
  
      default:
        return state;
    }
  }