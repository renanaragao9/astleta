import IndexField from './components/IndexField'
import DetailField from './components/DetailField'
import FormField from './components/FormField'

Nova.booting((app, store) => {
  app.component('index-cpf', IndexField)
  app.component('detail-cpf', DetailField)
  app.component('form-cpf', FormField)
})
