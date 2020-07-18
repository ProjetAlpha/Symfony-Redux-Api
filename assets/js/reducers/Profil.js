const defaultState = {
    profil: {},
    loading: false,
}
  
export default function Profil(state = defaultState, action = {}) {
    switch (action.type) {

        case 'FETCH_PROFIL_DATA': {
          return {
            ...state,
            profil: action.data
          }
        }

        // remove profil data

        // update profil data

        default:
            return state;
    }
}