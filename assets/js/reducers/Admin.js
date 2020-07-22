const initialState = {
    users: null,
}
  
export default function Admin(state = initialState, action = {}) {
    switch (action.type) {
     
      case 'GET_USERS': {
        return {
          ...state,
          users: action.data
        }
      }
  
      case 'REMOVE_USER': {
        return {
            ...state,
            users: state.users.filter(item => item.id !== action.data.id)
        }
      }
  
      default:
        return state;
    }
  }