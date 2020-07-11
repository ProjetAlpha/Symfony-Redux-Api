import { connect } from 'react-redux';

class LatestNewsHome extends Component {
    constructor() {
      super();
    }
  
    componentDidMount() {
    }
  
    render() {
      return (
        <div>Coucou</div>
      );
    }
  }
  
  LatestNewsHome.propTypes = {
  };
  
  const mapStateToProps = (state) => ({
  });
  
export default connect(mapStateToProps)(LatestNewsHome);  