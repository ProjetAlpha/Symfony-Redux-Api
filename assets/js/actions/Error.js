export const loadErrors = error => ({
    type: 'RESPONSE_ERROR',
    error
});
    
export const clearError = () => ({
    type: 'CLEAR_ERROR'
});