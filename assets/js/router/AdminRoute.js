import React from 'react';
import { Route, Redirect } from 'react-router-dom';
import { isAdmin } from '../utils/Authentification';

const AdminRoute = ({component: Component, extraProps, ...rest}) => {
    return (

        // Show the component only when the user is logged in
        // Otherwise, redirect the user to /signin page
        <Route {...rest} render={routeProps => (
            console.log(routeProps),
            isAdmin() ?
                <Component {...routeProps} />
            : <Redirect to="/register" />
        )} />
    );
};

export default AdminRoute;