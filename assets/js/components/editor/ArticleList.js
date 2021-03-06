import React, { Component } from "react";
import { connect } from "react-redux";
import { withRouter } from 'react-router-dom';
import { makeStyles, withStyles } from '@material-ui/core/styles';
import { convertToRaw, EditorState, AtomicBlockUtils, RichUtils, getDefaultKeyBinding, convertFromRaw, CompositeDecorator, Editor } from "draft-js";
import { Link } from 'react-router-dom';

import * as UI from '../../UI/Admin/base';
import * as Auth from '../../utils/Authentification';
import Pagination from '../main/Pagination';
import AdminStyle from '../../UI/Admin/style';
import { fetchAllArticle, deleteArticle } from '../../actions/Admin';
import { fetchImage } from '../../actions/Image';
import Search from '../main/Search';
import CustomDialog from '../main/CustomDialog';
import PropTypes from 'prop-types';

class ArticleList extends React.Component {

    state = {
        articles: [],
        article: null,
        highlightArticles: [],
        loading: true,
        isDraft: false,
        images: [],
        loadingImages: [],
        triggerDialog: false
    }

    componentDidMount() {
        this.props.fetchAllArticle(this.props.user.id, {
            is_draft: this.props.isDraft
        });
    }

    UNSAFE_componentWillReceiveProps(nextProps) {
        if (nextProps.articles !== this.props.articles) {
            this.setState({
                articles: nextProps.articles,
                loading: false
            })
        }
    }

    update(articles) {
        this.setState({
            articles: articles
        })
    }

    reset(articles) {
        this.update(articles);
        this.setState({
            highlightArticles: []
        })
    }

    loadImage(id, index) {
        if (!id || this.state.images[id]) return;

        const isImageLoading = this.state.loadingImages.find(images => images.id == id);

        if (!isImageLoading) {
            this.setState(prevState => ({
                loadingImages: [...prevState.loadingImages, {
                    id: id
                }]
            }));

            fetchImage(id).then(res => {
                if (!res || !res.data) return;

                let images = [...this.state.images];
                images[id] = res.data.image;

                this.setState({
                    images: images,
                });
            });
        }
    }

    getLink(to) {
        return props => <Link to={to} {...props} />;
    }

    findArticleMatch(search, article, articleIndex) {
        return -1 !== article.title.toLowerCase().indexOf(search) || -1 !== article.description.toLowerCase().indexOf(search);
    }

    handleDelete(article) {
        this.props.deleteArticle(this.props.user.id, article.id, article.cover_id);
        this.setState({
            triggerDialog: false
        })
    }

    naviguate(url) {
        this.props.history.push(url);
    }

    render() {
        const classes = this.props.classes;

        return (
            <div className={classes.root}>
                <UI.List className={classes.root}>
                    <UI.ListItem className={classes.searchItem}>
                        <Search
                            filterData={(search, article, index) => this.findArticleMatch(search, article, index)}
                            data={this.state.articles}
                            update={(articles) => this.update(articles)}
                            reset={(articles) => this.reset(articles)}
                            isLoading={this.state.loading}
                        />
                    </UI.ListItem>
                    {this.state.articles && <Pagination baseUrl={'/articles'} maxItem={5} data={this.state.articles} render={
                        (article, index, position) => (
                            <div key={position}>
                                { /* <div dangerouslySetInnerHTML={{ __html: this.state.highlightArticles[position] || article.raw_data }}></div> */}
                                <UI.ListItem>
                                    <UI.Grid container sm spacing={2}>
                                        <UI.Grid item xs={12} md={3} sm={3} component={Link} to={"#"}
                                            onClick={() => this.props.history.push(`/articles/${article.id}/view`)}>
                                            {
                                                article.cover_id &&
                                                this.loadImage(article.cover_id, position)
                                            }
                                            {
                                                this.state.images[article.cover_id]
                                                && <img className={classes.img} alt="complex" src={this.state.images[article.cover_id]} />
                                            }
                                            {
                                                !this.state.images[article.cover_id] && article.cover_id
                                                && <div className={classes.circularContainer} variant="outlined" >
                                                    <UI.CircularProgress />
                                                </div>
                                            }
                                            {
                                                !article.cover_id && 
                                                <UI.Paper className={classes.circularContainer}>
                                                    <UI.VisibilityOffIcon>
                                                    </UI.VisibilityOffIcon>
                                                </UI.Paper>
                                            }
                                        </UI.Grid>
                                        <UI.Grid item sm={8} md={8} xs={12} sm container>
                                            <UI.Grid item xs container direction="column" component={Link} to={"#"}
                                                onClick={() => this.props.history.push(`/articles/${article.id}/view`)}>
                                                <UI.Grid item xs>
                                                    <UI.Typography gutterBottom variant="h4">
                                                        {article.title}
                                                    </UI.Typography>
                                                    <UI.Typography variant="body1" color="textSecondary">
                                                        {article.description}
                                                    </UI.Typography>
                                                </UI.Grid>
                                            </UI.Grid >
                                        </UI.Grid>
                                        {Auth.isAdmin() && <UI.Grid item className={classes.mobile_row}>
                                            <UI.Grid item>
                                                <UI.IconButton onClick={() => this.props.history.push(`/articles/${article.id}/edit`)}>
                                                    <UI.CreateIcon color="primary" />
                                                </UI.IconButton>
                                            </UI.Grid>
                                            <UI.Grid item>
                                                <UI.IconButton onClick={() => this.setState({ article: article, triggerDialog: true })}>
                                                    <UI.DeleteIcon color="secondary" />
                                                </UI.IconButton>
                                            </UI.Grid>
                                        </UI.Grid>}
                                    </UI.Grid>
                                </UI.ListItem>
                                {
                                    <UI.Divider component="li" />
                                }
                            </div>
                        )
                    }
                    />
                    }
                </UI.List>
                <CustomDialog open={this.state.triggerDialog ? true : false}
                    onConfirmation={this.handleDelete.bind(this, this.state.article)}
                    onClose={() => this.setState({ triggerDialog: false })}
                    text={'Are you sure you want to delete this article ?'}
                />
            </div>
        );
    }
}

ArticleList.propTypes = {
    isDraft: PropTypes.bool.isRequired
}

const mapStateToProps = state => {
    return {
        error: state.Error.error,
        user: state.Authentification.user,
        users: state.Admin.users,
        articles: state.Admin.articles
    };
};

const style = withStyles(AdminStyle)(ArticleList);

const router = withRouter(style);

export default connect(mapStateToProps, { fetchAllArticle, fetchImage, deleteArticle })(router);