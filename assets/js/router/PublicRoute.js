import React from 'react';
import { Route, Redirect } from 'react-router-dom';
import { isLogin, isAdmin }  from '../utils/Authentification';

const PublicRoute = ({component: Component, restricted, ...rest}) => {
    return (
        // restricted = false meaning public route
        // restricted = true meaning restricted route
        <Route {...rest} render={props => (
            isLogin() && restricted ?
                <Redirect to={!isAdmin() ? "/profil" : "/admin"}/>
            : <Component {...props} />
        )} />
    );
};

export default PublicRoute;