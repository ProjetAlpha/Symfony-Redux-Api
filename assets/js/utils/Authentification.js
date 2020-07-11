

const exception = (message, name) => { this.message = message; this.name = name; };

export function isLogin() {
    const user = localStorage.getItem('user');
    return user !== null && user.email !== null && user.id !== null;
}

export function setUser(user) {
    if (localStorage.getItem('user') === null)
        localStorage.setItem(user);
    else
        throw new expection('User already in localStorage.', 'User exception');
}