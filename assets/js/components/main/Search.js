import React, { Component } from "react";
import * as UI from '../../UI/Search/base';
import { withStyles } from '@material-ui/core/styles';
import SearchStyle from '../../UI/Search/style';
import PropTypes from 'prop-types';

class Search extends React.Component {

    state = {
        initialData: [],
        textInput: ''
    }
    
    filterData(e) {
        const text = e.target.value;
        if (text == '') {
            return this.reset();
        }

        this.setState({
            textInput: text
        });
        const results = this.state.initialData.filter((value, index) => this.props.filterData(text, value, index));

        this.props.update(results);
    }

    UNSAFE_componentWillReceiveProps(nextProps) {
        if (nextProps.data !== this.props.data) {
            if (this.state.initialData.length === 0) {
                this.setState({
                    initialData: [...nextProps.data]
                });
            }
        }
    }

    reset() {
        if (!this.state.initialData) return false;
        this.props.reset(this.state.initialData);
        this.setState({
            textInput: ''
        });
    }

    render() {
        const classes = this.props.classes;

        return (
            <div>
                {
                    this.props.isLoading && <UI.CircularProgress />
                }
                { !this.props.isLoading && <div className={classes.search}>

                    <div className={classes.searchIcon}>
                        <UI.SearchIcon />
                    </div>
                    <UI.InputBase
                        placeholder="Search…"
                        classes={{
                            root: classes.inputRoot,
                            input: classes.inputInput,
                        }}
                        value={this.state.textInput}
                        onChange={this.filterData.bind(this)}
                        inputProps={{ 'aria-label': 'search' }}
                        endAdornment={<UI.ClearIcon style={{ cursor: 'pointer' }} onClick={this.reset.bind(this)} />}
                    />
                </div>
                }
            </div>
        );
    }
}

Search.propTypes = {
    data: PropTypes.array.isRequired,
    filterData: PropTypes.func.isRequired,
    reset: PropTypes.func.isRequired,
    update: PropTypes.func.isRequired
}

export default withStyles(SearchStyle)(Search);
