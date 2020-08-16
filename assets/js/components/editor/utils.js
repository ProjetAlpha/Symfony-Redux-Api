export function getBlocksWhereEntityData(editorState, filter) {
    const contentState = editorState.getCurrentContent();
    return contentState.get('blockMap').filter(block => {
      const entityData = block.getEntityAt(0)
        ? contentState.getEntity(block.getEntityAt(0)).getData()
        : null;
      return entityData && filter(entityData);
    });
  }

export function getEntities(editorState, entityType = null) {
  const content = editorState.getCurrentContent();
  const entities = [];
  content.getBlocksAsArray().forEach((block) => {
      let selectedEntity = null;
      console.log(block);
      block.findEntityRanges(
          (character) => {
              if (character.getEntity() !== null) {
                  const entity = content.getEntity(character.getEntity());
                  if (!entityType || (entityType && entity.getType() === entityType)) {
                      selectedEntity = {
                          entityKey: character.getEntity(),
                          blockKey: block.getKey(),
                          entity: content.getEntity(character.getEntity()),
                      };
                      return true;
                  }
              }
              return false;
          },
          (start, end) => {
              entities.push({...selectedEntity, start, end});
          });
  });
  return entities;
};