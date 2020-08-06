import * as Auth from '../utils/Authentification';

const isConfirmed = Auth.getUser('isConfirmed') === true;

const initialState = {
    isConfirmed: isConfirmed,
    success: false
}
  
export default function Settings(state = initialState, action = {}) {
    switch (action.type) {
        
        case 'CONFIRM_ACCOUNT': {
            return {
                ...state,
                isConfirmed: true
            }
        }

        case 'CONFIRM_RESET_PASSWORD': {
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