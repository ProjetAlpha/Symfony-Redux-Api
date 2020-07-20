const initialState = {
  user: localStorage.getItem('user') ? JSON.parse(localStorage.getItem('user')) : null,
  success: false
}

export default function Authentification(state = initialState, action = {}) {
  switch (action.type) {
    
    case 'LOGIN': {
      return {
        ...state,
        user: action.data,
        success: true
      }
    }

    case 'LOGOUT': {
      return {
        ...state,
        success: true
      }
    }

    case 'REGISTER': {
      return {
        ...state,
        success: true
      }
    }

    case 'RESET_SUCCESS': {
      return {
        ...state,
        success: false
      }
    }

    default:
      return state;
  }
}