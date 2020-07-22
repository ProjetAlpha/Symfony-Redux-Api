import { combineReducers } from 'redux';

import Image from './Image';
import Authentification from './Authentification';
import Error from './Error';
import Profil from './Profil';
import Admin from './Admin';

export default combineReducers({
  Authentification,
  Image,
  Error,
  Profil,
  Admin
});