import ability from './ability'

export const canNavigate = to => {
    if(to.meta.resource)
        return to.matched.some(route => ability.can(route.meta.action || 'read', route.meta.resource))
    return true
}

export const _ = undefined
