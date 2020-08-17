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
    else {
        const user = { ...JSON.parse(localStorage.getItem('user')), ...user };
        localStorage.setItem('user', JSON.stringify(user));
    }
}

export const updateUser = (key, value) => {
    if (localStorage.getItem('user')) {
        let user = JSON.parse(localStorage.getItem('user'));
        if (user[key])
            user[key] = value;
        localStorage.setItem('user', JSON.stringify(user));
    }
}

export const getUser = (key = false) => {
    if (!isLogin()) {
        return false;
    }
    
    const user = JSON.parse(localStorage.getItem('user'));
    if (key && user.hasOwnProperty(key))
        return user[key];

    return JSON.parse(localStorage.getItem('user'));
}

export const logout = () => {
    if (localStorage.getItem('user')) {
        localStorage.removeItem('user');
        window.location = '/';
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

export const setAuthToken = (axios, token) => {
    axios.defaults.headers.common['X-API-TOKEN'] = '';
    delete axios.defaults.headers.common['X-API-TOKEN'];
  
    if (token) {
      axios.defaults.headers.common['X-API-TOKEN'] = `${token}`;
    }
}