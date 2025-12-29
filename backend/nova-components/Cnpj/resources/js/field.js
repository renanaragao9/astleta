import IndexField from './components/IndexField'
import DetailField from './components/DetailField'
import FormField from './components/FormField'

Nova.booting((app, store) => {
  app.component('index-cnpj', IndexField)
  app.component('detail-cnpj', DetailField)
  app.component('form-cnpj', FormField)
})
