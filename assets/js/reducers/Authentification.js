let user = localStorage.getItem('user');
const initialState = user ? user : {};

export default function Authentification(state = initialState, action = {}) {
  switch (action.type) {
    
    case 'LOGIN': {
      return {
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