import React, { Component } from "react";
import { connect } from "react-redux";
import { makeStyles, withStyles } from '@material-ui/core/styles';
import { Link } from 'react-router-dom';

import * as UI from '../../UI/Admin/base';
import * as Auth from '../../utils/Authentification';
import AdminStyle from '../../UI/Admin/style';
import { fetchArticle, deleteArticle } from '../../actions/Admin';
import { fetchImage } from '../../actions/Image';
import CustomDialog from '../main/CustomDialog';
import PropTypes from 'prop-types';

class Article extends React.Component {

    state = {
        id: null,
        article: null,
        image: null,
        loading: true,
        triggerDialog: false,
        triggerSnack: false
    }

    componentDidMount() {
        const { articleId } = this.props.match.params;

        this.setState({
            id: articleId
        });

        this.props.fetchArticle(this.props.user.id, articleId);
    }

    UNSAFE_componentWillReceiveProps(nextProps) {
        if (nextProps.article !== this.props.article) {
            this.setState({
                article: nextProps.article,
                loading: false
            })
        }
        if (nextProps.articles !== this.props.articles) {
            // 
        }
    }

    loadImage(id) {
        if (!this.state.image && id) {
            fetchImage(id).then(res => {
                this.setState({
                    image: res.data.image
                });
            });
        }
    }

    handleDialog() {
        this.setState(prevState => ({
            triggerDialog: !prevState.triggerDialog
        }))
    }

    getLink(to) {
        return props => <Link to={to} {...props} />;
    }

    handleDelete(article) {
        this.props.deleteArticle(this.props.user.id, article.id, article.cover_id);
        this.props.history.goBack();
    }

    render() {
        const { article, classes } = this.props;

        return (
            <div className={classes.root}>
                <UI.List className={classes.root}>
                    {
                        article && <UI.Grid>
                            <div className={classes.imgContainer}>
                                {article.cover_id && <UI.Grid item>
                                    {
                                        this.loadImage(article.cover_id)
                                    }
                                    {
                                        this.state.image && <img className={classes.img} alt="complex" src={this.state.image} />
                                    }
                                </UI.Grid>
                                }
                            </div>
                            {Auth.isAdmin() && <UI.Grid container spacing={6} direction="row" justify="center">
                                <UI.Grid item>
                                    <UI.Button size="medium" variant="contained" color="primary" startIcon={<UI.CreateIcon />}>
                                        Edit
                                </UI.Button>
                                </UI.Grid>
                                <UI.Grid item>
                                    <UI.Button size="medium" variant="contained" color="secondary" startIcon={<UI.DeleteIcon />} onClick={() => this.setState({ triggerDialog: true })}>
                                        Delete
                                    </UI.Button>
                                </UI.Grid>
                            </UI.Grid>}
                            <UI.Grid item sm container direction="row">
                                <UI.Grid item xs container direction="column" spacing={2}>
                                    <UI.Grid item xs className={classes.header}>
                                        <UI.Typography gutterBottom variant="h4">
                                            {article.title}
                                        </UI.Typography>
                                        <UI.Typography variant="body1" color="textSecondary">
                                            {article.description}
                                        </UI.Typography>
                                    </UI.Grid>
                                </UI.Grid >
                            </UI.Grid>
                            <UI.Grid item sm container>
                                <div dangerouslySetInnerHTML={{ __html: article.raw_data }}></div>
                            </UI.Grid>
                        </UI.Grid>
                    }
                </UI.List>
                <CustomDialog open={this.state.triggerDialog ? true : false}
                    onConfirmation={this.handleDelete.bind(this, article)}
                    onClose={this.handleDialog.bind(this)}
                    text={'Are you sure you want to delete this article ?'}
                />
            </div>
        );
    }
}

const style = withStyles(AdminStyle)(Article);

const mapStateToProps = state => {
    return {
        error: state.Error.error,
        user: state.Authentification.user,
        article: state.Admin.article,
        articles: state.Admin.articles
    };
};

export default connect(mapStateToProps, { fetchArticle, fetchImage, deleteArticle })(style);
