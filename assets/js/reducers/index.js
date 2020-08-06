import { combineReducers } from 'redux';
import { connectRouter } from 'connected-react-router'

import Image from './Image';
import Authentification from './Authentification';
import Error from './Error';
import Success from './Success';
import Profil from './Profil';
import Admin from './Admin';
import Drawer from './main/Drawer';
import Settings from './Settings';

const createRootReducer = (history) => combineReducers({
  router: connectRouter(history),
  Authentification,
  Image,
  Error,
  Profil,
  Admin,
  Drawer,
  Settings,
  Success
})

export default createRootReducer;