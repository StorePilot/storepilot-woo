import Quill from 'quill'
let Embed = Quill.import('blots/block/embed')

class More extends Embed {
  static create (value) {
    return super.create(value)
  }
}

More.blotName = 'more'
More.tagName = 'MORE'

export default More
