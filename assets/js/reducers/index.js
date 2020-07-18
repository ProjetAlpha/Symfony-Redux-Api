import { combineReducers } from 'redux';

import Image from './Image';
import Authentification from './Authentification';
import Error from './Error';
import Profil from './Profil';

export default combineReducers({
  Authentification,
  Image,
  Error,
  Profil
});