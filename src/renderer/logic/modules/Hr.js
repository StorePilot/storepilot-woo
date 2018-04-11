import Quill from 'quill'
let Embed = Quill.import('blots/block/embed')

class Hr extends Embed {
  static create (value) {
    return super.create(value)
  }
}

Hr.blotName = 'hr'
Hr.tagName = 'HR'

export default Hr
