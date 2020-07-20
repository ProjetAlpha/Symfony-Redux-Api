export const isLogin = () => {
    const user = JSON.parse(localStorage.getItem('user'));
    return user && user.email && user.id;
}

export const setUser = (user) => {
    if (!localStorage.getItem('user') || localStorage.getItem('user') == 'null'){
       localStorage.setItem('user', JSON.stringify(user));
    }
    else
        return null;
}

export const getUser = () => {
    if (!isLogin()) {
        return false;
    }
    
    return JSON.parse(localStorage.getItem('user'));
}

export const logout = () => {
    if (localStorage.getItem('user')) {
        localStorage.removeItem('user');
    }
    else
        return null;
}