package service;

import helper.HibernateUtil;

import java.lang.reflect.InvocationTargetException;
import java.util.List;

import javax.persistence.EntityManager;
import javax.ws.rs.Consumes;
import javax.ws.rs.DELETE;
import javax.ws.rs.GET;
import javax.ws.rs.POST;
import javax.ws.rs.PUT;
import javax.ws.rs.Path;
import javax.ws.rs.PathParam;
import javax.ws.rs.Produces;
import javax.ws.rs.core.MediaType;

import model.Series;

import org.apache.commons.beanutils.BeanUtils;

@Path("/series")
public class SeriesService {
	@GET
	@Produces({MediaType.APPLICATION_JSON})
	public List<Series> getSeries() {
		List<Series> series = HibernateUtil.getResults("select s from Series s", null);
		return series;
	}
	
	@PUT
	@Path("{id}")
	@Produces(MediaType.APPLICATION_JSON)
	@Consumes(MediaType.APPLICATION_JSON)
	public String updateSeries(Series s, @PathParam("id") Long id) {
		EntityManager em = HibernateUtil.getEntityManagerFactory().createEntityManager();
		Series existing = em.find(Series.class, id);
		if (existing == null) {
			System.err.println("Unable to find User with ID = " + id);
			return "{success: false, message: 'Unable to find User', data: []}";
		}
		
		em.getTransaction().begin();
		
		try {
			BeanUtils.copyProperties(existing, s);
		} catch (IllegalAccessException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (InvocationTargetException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		
		em.persist(existing);
		em.getTransaction().commit();
		return "{success: true, message: 'Updated', data: []}";
	}
	
	@POST
	@Produces(MediaType.APPLICATION_JSON)
	@Consumes(MediaType.APPLICATION_JSON)
	public String addSeries(Series s) {
		EntityManager em = HibernateUtil.getEntityManagerFactory()
				.createEntityManager();
		em.getTransaction().begin();
		em.persist(s);
		em.getTransaction().commit();
		return "{success: true, message: 'Added', data: []}";
	}
	
	@DELETE
	@Path("/{id}")
	@Produces(MediaType.APPLICATION_JSON)
	@Consumes(MediaType.APPLICATION_JSON)
	public String deleteSeries(@PathParam("id") Long id) {
		EntityManager em = HibernateUtil.getEntityManagerFactory().createEntityManager();
		Series existing = em.find(Series.class, id);
		if (existing == null) {
			System.err.println("Unable to find User with ID = " + id);
			return "{success: false, message: 'Unable to find User', data: []}";
		}
		
		int result = HibernateUtil.execute("delete from User u where u.id = ?", 
				new Object[] {id});
		System.out.println("RESULT = " + result);
		return "{success: true, message: 'Deleted', data: []}";
	}
	
}
