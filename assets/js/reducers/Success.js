import { LOCATION_CHANGE } from 'connected-react-router';

const initialState = {
    success: null
};

export default function Success(state = initialState, action) {
      switch (action.type) {
    
        case 'REGISTER_SUCCESS':
          return {
            ...state,
            success: true
          };
    
        case 'RESET_SUCCESS':
          return initialState;
    
        case LOCATION_CHANGE:
          return initialState;
  
        default:
          return state;
      }
  }