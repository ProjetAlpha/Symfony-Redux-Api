const initialState = {
  error: null
};

export default function Error(state = initialState, action) {
    switch (action.type) {
  
      case 'ADD_ERROR':
        return {
          ...state,
          error: action.error
        };
  
      case 'CLEAR_ERROR':
        return initialState;
  
      default:
        return state;
    }
}