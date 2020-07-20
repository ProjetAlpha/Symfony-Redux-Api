export const getError = (state, name = null) => {
    if (!state.error) return false;

    // global form error
    if (!name && state.error.error) return state.error.error;
    
     // input form error
     if (state.error.hasOwnProperty(name))
        return state.error[name];

    return false;
}