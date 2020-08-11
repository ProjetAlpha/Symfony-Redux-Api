import React, { Component } from "react";
import { connect } from "react-redux";
import { makeStyles, withStyles } from '@material-ui/core/styles';
import { convertToRaw, EditorState, AtomicBlockUtils, RichUtils, getDefaultKeyBinding, convertFromRaw, CompositeDecorator, Editor } from "draft-js";
//import Editor,  { composeDecorators } from 'draft-js-plugins-editor';

import * as UI from '../../UI/Admin/base';
import Pagination from '../main/Pagination';
import AdminStyle from '../../UI/Admin/style';
import { fetchAllArticle } from '../../actions/Admin';

function findImageEntities(contentBlock, callback, contentState) {
    contentBlock.findEntityRanges(character => {
      const entityKey = character.getEntity();
      return (
        entityKey !== null &&
        contentState.getEntity(entityKey).getType() === "IMAGE"
      );
    }, callback);
  }
  
  const Image = props => {
    console.log(props);
    const { height, src, width } = props.contentState.getEntity(props.entityKey).getData();
    return <img src={src} height={height} width={width} />;
  };


class ArticleList extends React.Component {

    _isMounted = false

    state = {
        editors: [],
    }

    componentDidMount() {
        this.props.fetchAllArticle(this.props.user.id, {
            is_draft: true
        });
        this._isMounted = true
    }

    componentWillUnmount() {
        this._isMounted = false
    }

    UNSAFE_componentWillReceiveProps(nextProps) {
        if (nextProps.articles !== this.props.articles) {
            if (nextProps.articles) {
                let editors = [];
                
                const decorator = new CompositeDecorator([
                    {
                      strategy: findImageEntities,
                      component: Image
                    }
                ]);
                
                for (let i = 0; i < nextProps.articles.length; i++) {
                    editors[i] = EditorState.createWithContent(convertFromRaw(JSON.parse(nextProps.articles[i].raw_data)), decorator);
                }
                
                this.setState({ editors: editors });
            } else {
                // error handler
                // this.props.history.push('/resetPassword');
            }
        }
    }

    onChange = (editorState) => {
        /*this.setState({
            editors[index]: editorState
        });*/
    };

    debug() {
        console.log(...arguments);
    }

    render() {
        const classes = this.props.classes;

        // 
        return (
                <div className={classes.root}>

                    <UI.List className={classes.root}>
                        {this.props.articles && <Pagination baseUrl={'/articles'} maxItem={5} data={this.props.articles} render={
                            (article, index, position) => (
                                <div key={index}>
                                    <UI.ListItem key={index}>
                                        { this.debug(position) }
                                        { this.state.editors[position] &&
                                            <Editor editorState={this.state.editors[position]}
                                                onChange={this.onChange} readOnly/>
                                        }  
                                    </UI.ListItem>
                                    <UI.Divider component="li" />
                                </div>
                            )
                        }
                        />
                        }
                    </UI.List>
                </div>
        );
    }
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

export default connect(mapStateToProps, { fetchAllArticle })(style);