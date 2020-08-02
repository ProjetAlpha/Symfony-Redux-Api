export const setData = (data) => {
    return dispatch => {
        dispatch({ type: 'SET_DATA', data: data })
    };
};

export const deleteData = () => {
    return dispatch => {
        dispatch({ type: 'UNSET_DATA' })
    };
};