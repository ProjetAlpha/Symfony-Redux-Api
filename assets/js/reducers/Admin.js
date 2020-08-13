const initialState = {
    users: null,
    articles: null,
    success: null
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

      case 'ADMIN_FETCH_ARTICLE': {
        return {
          ...state,
          articles: [...state.articles, action.data],
          success: true
        }
      }

      case 'ADMIN_CREATE_ARTICLE': {
        return {
          ...state,
          articles: action.data,
          success: true
        }
      }

      case 'ADMIN_UPDATE_ARTICLE': {
        return {
          ...state,
          articles: action.data,
          success: true
        }
      }

      case 'ADMIN_FETCH_ALL_ARTICLE': {
        return {
          ...state,
          articles: action.data,
          success: true
        }
      }
  
      default:
        return state;
    }
  }