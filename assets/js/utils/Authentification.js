export const isLogin = () => {
    const user = JSON.parse(localStorage.getItem('user'));
    return user && user.email && user.id;
}

export const isAdmin = () => {
    const user = JSON.parse(localStorage.getItem('user'));
    return user && user.email && user.id && user.isAdmin;
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

export const logoutOnResponseError = (code) => {
    // forbidden or unauthorized automatically logout user
    if (code === 403 || code == 401) {
        logout();
    }
}