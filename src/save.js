import { useBlockProps } from '@wordpress/block-editor';

export const Save = ( {
  style,
  attributes: {
    buttonIcon
  }
  } ) => {
  return (
    <div { ...useBlockProps.save( {
      style: { style },
    } ) }
      id={"pls-language-switcher"}
    >
      <i dangerouslySetInnerHTML={{__html: buttonIcon}} ></i>
      [pls_switcher]
    </div>
  );
};
