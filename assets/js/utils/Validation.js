import { getError } from "./Error";

export const setHelperText = (props, message = null) => {
    if (getError(props, 'email'))
        return getError(props, 'email');

    if (props.success && message)
        return message;

    return '';
}