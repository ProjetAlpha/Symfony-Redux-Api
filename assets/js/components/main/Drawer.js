import React from 'react';
import { Link } from 'react-router-dom';
import { connect } from "react-redux";

import * as Constant from './Constant';
import * as Auth from '../../utils/Authentification';
import * as UI from '../../UI/NavBar/base';
import { setData, deleteData } from '../../actions/main/Drawer';

import NavBarStyle from '../../UI/NavBar/style';
import { withStyles, withTheme } from '@material-ui/core/styles';

let drawerContent = [];

drawerContent[Constant.ADMIN] = [];
drawerContent[Constant.ANONYMOUS] = [];
drawerContent[Constant.USER] = [];

drawerContent[Constant.ADMIN].push({
    type: Constant.ADMIN,
    icon: <UI.PeopleIcon></UI.PeopleIcon>,
    primary: 'Users',
    route: '/admin'
});

drawerContent[Constant.ADMIN].push({
    type: Constant.ADMIN,
    icon: <UI.CreateIcon></UI.CreateIcon>,
    primary: 'Articles',
    route: '/articles'
});

drawerContent[Constant.ADMIN].push({
    type: Constant.ADMIN,
    icon: <UI.AddCircleIcon></UI.AddCircleIcon>,
    primary: 'New article',
    route: '/articles/new'
})

drawerContent[Constant.USER].push({
    type: Constant.USER,
    icon: <UI.AccountCircleIcon></UI.AccountCircleIcon>,
    primary: 'Profil',
    route: '/profil'
})

drawerContent[Constant.ANONYMOUS].push({
    type: Constant.ANONYMOUS,
    icon: <UI.HomeIcon></UI.HomeIcon>,
    primary: 'Home',
    route: '/'
})

class Drawer extends React.Component {

    state = {

    }

    getUserTags(type) {
        return (
            this.props.drawerContent
            && this.props.drawerContent[type]
            && this.props.drawerContent[type].map((data, index) => (
                <Link to={data.route} key={index}>
                    <UI.ListItem button>
                        <UI.ListItemIcon>
                            {data.icon}
                        </UI.ListItemIcon>
                        <UI.ListItemText primary={data.primary} />
                    </UI.ListItem>
                </Link>
            ))
        );
    }

    componentDidMount() {
        if (!this.props.drawerContent)
            this.props.setData(drawerContent);
    }

    render() {
        const classes = this.props.classes;

        return (
            <div className={classes.drawerContainer}>
                <div className={classes.toolbar} />
                <UI.Divider />
                <UI.List>

                    {
                        this.getUserTags(Constant.ANONYMOUS)
                    }

                    {Auth.isAdmin() || Auth.isLogin() ? <UI.Divider /> : ''}

                    {
                        Auth.isAdmin() && this.getUserTags(Constant.ADMIN)
                    }

                    {Auth.isLogin() && <UI.Divider />}

                    {Auth.isLogin() && this.getUserTags(Constant.USER)}

                </UI.List>
            </div>
        );
    }
}

const mapStateToProps = state => {
    return {
        user: state.Authentification.user,
        drawerContent: state.Drawer.content
    };
};

const AppBarTheme = withTheme(Drawer);

const AppBarStyle = withStyles(NavBarStyle)(AppBarTheme);

export default connect(mapStateToProps, { setData, deleteData })(AppBarStyle);