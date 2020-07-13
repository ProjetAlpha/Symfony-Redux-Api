const initialState = {
  user: localStorage.getItem('user') ? JSON.parse(localStorage.getItem('user')) : null
}

export default function Authentification(state = initialState, action = {}) {
  switch (action.type) {
    
    case 'LOGIN': {
      return {
        ...state,
        user: action.data
      }
    }

    case 'LOGOUT': {
      return { }
    }

    case 'REGISTER': {
      return { }
    }

    default:
      return state;
  }
}